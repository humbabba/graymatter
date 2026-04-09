@props(['id', 'name', 'value' => ''])

<div x-data="tiptapEditor('{{ $id }}')">
    <input id="{{ $id }}" type="hidden" name="{{ $name }}" value="{{ $value }}">

    <div class="tiptap-toolbar">
        {{-- Text formatting --}}
        <div class="tiptap-toolbar__group">
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('bold') }" @click="toggleBold()" title="Bold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 4h8a4 4 0 0 1 0 8H6zM6 12h9a4 4 0 0 1 0 8H6z"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('italic') }" @click="toggleItalic()" title="Italic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('underline') }" @click="toggleUnderline()" title="Underline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 3v7a6 6 0 0 0 12 0V3"/><line x1="4" y1="21" x2="20" y2="21"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('strike') }" @click="toggleStrike()" title="Strikethrough">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><path d="M16 6C16 6 14.5 4 12 4 9 4 7 6 7 8c0 1.5 1 3 5 4m-1 8c3 0 5-2 5-4 0-1-.5-2-2-3"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('code') }" @click="toggleCode()" title="Inline code">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </button>
        </div>

        {{-- Headings --}}
        <div class="tiptap-toolbar__group">
            @for($i = 1; $i <= 6; $i++)
                <button type="button"
                        class="tiptap-toolbar__btn tiptap-toolbar__heading"
                        :class="{ 'is-active': isActive('heading', { level: {{ $i }} }) }"
                        @click="toggleHeading({{ $i }})"
                        title="Heading {{ $i }}"
                        style="font-size: {{ 15 - $i }}px;">H{{ $i }}</button>
            @endfor
        </div>

        {{-- Alignment --}}
        <div class="tiptap-toolbar__group">
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isAligned('left') }" @click="setTextAlign('left')" title="Align left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isAligned('center') }" @click="setTextAlign('center')" title="Align center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isAligned('right') }" @click="setTextAlign('right')" title="Align right">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>

        {{-- Lists & blocks --}}
        <div class="tiptap-toolbar__group">
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('bulletList') }" @click="toggleBulletList()" title="Bullet list">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('orderedList') }" @click="toggleOrderedList()" title="Ordered list">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><text x="2" y="8" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">1</text><text x="2" y="14" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">2</text><text x="2" y="20" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">3</text></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('blockquote') }" @click="toggleBlockquote()" title="Blockquote">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('codeBlock') }" @click="toggleCodeBlock()" title="Code block">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><polyline points="9 8 5 12 9 16"/><polyline points="15 8 19 12 15 16"/></svg>
            </button>
        </div>

        {{-- Insert --}}
        <div class="tiptap-toolbar__group">
            <button type="button" class="tiptap-toolbar__btn" :class="{ 'is-active': isActive('link') }" @click="setLink()" title="Link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" @click="uploadImage()" title="Upload image">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </button>
        </div>

        {{-- History --}}
        <div class="tiptap-toolbar__group">
            <button type="button" class="tiptap-toolbar__btn" @click="undo()" title="Undo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
            </button>
            <button type="button" class="tiptap-toolbar__btn" @click="redo()" title="Redo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.13-9.36L23 10"/></svg>
            </button>
        </div>
    </div>

    <div class="tiptap-editor" x-ref="editor"></div>
</div>
