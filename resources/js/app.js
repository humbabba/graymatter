import Alpine from 'alpinejs';
import './ajax-save';
import './tiptap-editor';
import { diffWords } from 'diff';

window.Alpine = Alpine;
window.diffWords = diffWords;

Alpine.start();
