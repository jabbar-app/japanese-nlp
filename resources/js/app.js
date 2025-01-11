import './bootstrap';
import { createApp } from 'vue';
import ParseComponent from './components/ParseComponent.vue';
import Flashcards from './components/Flashcards.vue';

const app = createApp({});
app.component('parse-component', ParseComponent);
app.component('flashcards', Flashcards);
app.mount('#app');
