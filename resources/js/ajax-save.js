/**
 * AJAX Save Component for Alpine.js
 * Provides form change detection, keyboard shortcuts, and AJAX submission.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('ajaxSave', () => ({
        form: null,
        isDirty: false,
        isSaving: false,
        justSaved: false,
        hasError: false,
        formExists: false,

        init() {
            this.form = document.querySelector('form[data-ajax-save]');
            this.formExists = !!this.form;

            if (!this.form) return;

            // Checkbox groups (json settings) share a name like "settings[key][]",
            // so keys must include the value to disambiguate boxes within a group.
            const checkboxKey = (el) => el.name + '__' + el.value + '__checked';

            this.initialFieldValues = {};
            this.form.querySelectorAll('input, textarea, select').forEach(el => {
                if (!el.name) return;
                if (el.type === 'checkbox') {
                    this.initialFieldValues[checkboxKey(el)] = el.checked;
                } else {
                    this.initialFieldValues[el.name] = el.value;
                }
            });

            const hasChanged = (target) => {
                if (!target.name) return true;
                if (target.type === 'checkbox') {
                    return this.initialFieldValues[checkboxKey(target)] !== target.checked;
                }
                return this.initialFieldValues[target.name] !== target.value;
            };

            this._checkboxKey = checkboxKey;

            this.form.addEventListener('input', (e) => {
                if (hasChanged(e.target)) this.markDirty();
            });
            this.form.addEventListener('change', (e) => {
                if (hasChanged(e.target)) this.markDirty();
            });

            // Listen for dynamically added/removed form fields
            setTimeout(() => {
                const observer = new MutationObserver(() => this.markDirty());
                observer.observe(this.form, { childList: true });
            }, 500);
        },

        markDirty() {
            this.isDirty = true;
            this.justSaved = false;
            this.hasError = false;
        },

        get statusText() {
            if (this.isSaving) return 'Saving...';
            if (this.justSaved) return 'Saved!';
            if (this.isDirty) return 'Unsaved changes';
            return '';
        },

        async save() {
            if (!this.form || !this.isDirty || this.isSaving) return;

            this.isSaving = true;
            this.hasError = false;

            const formData = new FormData(this.form);
            const action = this.form.getAttribute('action');

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.isDirty = false;
                    this.justSaved = true;

                    this.form.querySelectorAll('input, textarea, select').forEach(el => {
                        if (!el.name) return;
                        if (el.type === 'checkbox') {
                            this.initialFieldValues[this._checkboxKey(el)] = el.checked;
                        } else {
                            this.initialFieldValues[el.name] = el.value;
                        }
                    });

                    this.clearErrors();

                    setTimeout(() => {
                        this.justSaved = false;
                    }, 2000);
                } else {
                    this.hasError = true;
                    if (data.errors) {
                        this.displayErrors(data.errors);
                    } else if (data.message) {
                        this.displayErrors({ general: [data.message] });
                    }
                }
            } catch (error) {
                this.hasError = true;
                this.displayErrors({ general: ['An error occurred while saving.'] });
            } finally {
                this.isSaving = false;
            }
        },

        clearErrors() {
            const errorContainer = this.form.closest('div').querySelector('.ajax-error-container');
            if (errorContainer) {
                errorContainer.remove();
            }
            const existingErrors = this.form.closest('div').querySelector('.mb-6.p-4.bg-graymatter-red\\/20');
            if (existingErrors) {
                existingErrors.remove();
            }
        },

        displayErrors(errors) {
            this.clearErrors();

            const formParent = this.form.parentElement;
            const errorHtml = document.createElement('div');
            errorHtml.className = 'ajax-error-container mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm';

            const messages = Object.values(errors).flat();
            errorHtml.innerHTML = messages.map(msg => `<p>${this.escapeHtml(msg)}</p>`).join('');

            formParent.insertBefore(errorHtml, this.form);
            errorHtml.scrollIntoView({ behavior: 'smooth', block: 'center' });
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }));
});
