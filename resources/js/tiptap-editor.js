/**
 * Tip Tap Editor — Alpine.js component
 *
 * Usage in Blade:
 *   <div x-data="tiptapEditor('inputId')"> ... </div>
 *
 * The hidden input (identified by inputId) stores the HTML and participates
 * in FormData via its name attribute. onUpdate keeps it synced.
 *
 * The Editor instance is stored on the DOM element (this.$el.__editor) to
 * avoid Alpine's reactive Proxy wrapping, which breaks ProseMirror's strict
 * state identity checks.
 */

import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import TextAlign from '@tiptap/extension-text-align'
import Image from '@tiptap/extension-image'
import Placeholder from '@tiptap/extension-placeholder'

// Extend Image with alignment and linking
function applyAlign(img, align) {
    if (align === 'center') {
        img.style.display = 'block';
        img.style.marginLeft = 'auto';
        img.style.marginRight = 'auto';
    } else if (align === 'right') {
        img.style.display = 'block';
        img.style.marginLeft = 'auto';
        img.style.marginRight = '';
    } else {
        img.style.display = '';
        img.style.marginLeft = '';
        img.style.marginRight = '';
    }
}

const LinkedImage = Image.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            href: { default: null },
            align: {
                default: null,
                renderHTML(attrs) {
                    if (attrs.align === 'center') return { style: 'display: block; margin-left: auto; margin-right: auto' };
                    if (attrs.align === 'right') return { style: 'display: block; margin-left: auto' };
                    return {};
                },
                parseHTML(el) {
                    const style = el.getAttribute('style') || '';
                    if (style.includes('margin-left: auto') && style.includes('margin-right: auto')) return 'center';
                    if (style.includes('margin-left: auto')) return 'right';
                    return null;
                },
            },
        };
    },
    addNodeView() {
        const parentNodeView = this.parent?.();
        if (!parentNodeView) return null;

        return (props) => {
            const view = parentNodeView(props);

            // Apply initial alignment
            const img = view.dom.querySelector('img');
            if (img) applyAlign(img, props.node.attrs.align);

            // Re-apply alignment on node update
            const originalUpdate = view.update?.bind(view);
            view.update = (node, decorations, innerDecorations) => {
                const result = originalUpdate?.(node, decorations, innerDecorations);
                if (result !== false && img) {
                    applyAlign(img, node.attrs.align);
                }
                return result;
            };

            return view;
        };
    },
    renderHTML({ HTMLAttributes }) {
        const { href, ...imgAttrs } = HTMLAttributes;
        if (href) {
            return ['a', { href, target: '_blank', rel: 'noopener' }, ['img', imgAttrs]];
        }
        return ['img', imgAttrs];
    },
    parseHTML() {
        return [
            {
                tag: 'a[href] > img[src]',
                getAttrs(el) {
                    return { href: el.parentElement?.getAttribute('href') };
                },
            },
            ...this.parent?.() || [],
        ];
    },
});

const editors = new WeakMap();

function getEditor(component) {
    return editors.get(component.$refs?.editor);
}

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', (inputId) => ({
        _tick: 0,

        init() {
            const hiddenInput = document.getElementById(inputId);
            const editorEl = this.$refs.editor;
            if (!editorEl) return;

            const editor = new Editor({
                element: editorEl,
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [1, 2, 3, 4, 5, 6] },
                        link: { openOnClick: false },
                    }),
                    TextAlign.configure({
                        types: ['heading', 'paragraph'],
                    }),
                    LinkedImage.configure({
                        inline: false,
                        allowBase64: false,
                        resize: {
                            enabled: true,
                            alwaysPreserveAspectRatio: true,
                        },
                    }),
                    Placeholder.configure({
                        placeholder: 'Start writing...',
                    }),
                ],
                content: hiddenInput?.value || '',
                onTransaction: () => {
                    this._tick++;
                },
                onUpdate: ({ editor }) => {
                    if (hiddenInput) {
                        hiddenInput.value = editor.getHTML();
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                },
            });

            editors.set(editorEl, editor);
        },

        destroy() {
            const editor = getEditor(this);
            if (editor) {
                editors.delete(this.$refs.editor);
                editor.destroy();
            }
        },

        // --- Toolbar actions ---

        toggleBold() { getEditor(this)?.chain().focus().toggleBold().run(); },
        toggleItalic() { getEditor(this)?.chain().focus().toggleItalic().run(); },
        toggleUnderline() { getEditor(this)?.chain().focus().toggleUnderline().run(); },
        toggleStrike() { getEditor(this)?.chain().focus().toggleStrike().run(); },
        toggleCode() { getEditor(this)?.chain().focus().toggleCode().run(); },

        toggleHeading(level) { getEditor(this)?.chain().focus().toggleHeading({ level }).run(); },
        setParagraph() { getEditor(this)?.chain().focus().setParagraph().run(); },

        toggleBulletList() { getEditor(this)?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { getEditor(this)?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote() { getEditor(this)?.chain().focus().toggleBlockquote().run(); },
        toggleCodeBlock() { getEditor(this)?.chain().focus().toggleCodeBlock().run(); },

        setTextAlign(alignment) {
            const editor = getEditor(this);
            if (!editor) return;
            if (editor.isActive('image')) {
                editor.chain().focus().updateAttributes('image', { align: alignment === 'left' ? null : alignment }).run();
            } else {
                editor.chain().focus().setTextAlign(alignment).run();
            }
        },
        unsetTextAlign() { getEditor(this)?.chain().focus().unsetTextAlign().run(); },

        undo() { getEditor(this)?.chain().focus().undo().run(); },
        redo() { getEditor(this)?.chain().focus().redo().run(); },

        setLink() {
            const editor = getEditor(this);
            if (!editor) return;

            // If an image is selected, link the image instead
            if (editor.isActive('image')) {
                const current = editor.getAttributes('image').href || '';
                const url = prompt('Image link URL:', current);
                if (url === null) return;
                editor.chain().focus().updateAttributes('image', { href: url || null }).run();
                return;
            }

            const url = prompt('URL:');
            if (url === null) return;
            if (url === '') {
                editor.chain().focus().unsetLink().run();
                return;
            }
            editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        },

        async uploadImage() {
            const editor = getEditor(this);
            if (!editor) return;

            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async () => {
                const file = input.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('Image must be under 5 MB.');
                    return;
                }

                const formData = new FormData();
                formData.append('image', file);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                try {
                    const response = await fetch('/projects/upload-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) throw new Error('Upload failed');
                    const data = await response.json();

                    editor.chain().focus().setImage({ src: data.url }).run();
                } catch {
                    alert('Image upload failed.');
                }
            };
            input.click();
        },

        // --- Active state checks for toolbar button styling ---

        isActive(name, attrs) {
            void this._tick; // reactive dependency
            return getEditor(this)?.isActive(name, attrs) ?? false;
        },

        isAligned(alignment) {
            void this._tick; // reactive dependency
            const editor = getEditor(this);
            if (!editor) return false;
            if (editor.isActive('image')) {
                const imgAlign = editor.getAttributes('image').align;
                return alignment === 'left' ? !imgAlign : imgAlign === alignment;
            }
            return editor.isActive({ textAlign: alignment });
        },
    }));
});
