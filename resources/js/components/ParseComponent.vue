<template>
    <div :class="['app-container', { 'dark-mode': isDarkMode }]">
        <!-- Info Modal Popup (muncul sekali saat pertama kali) -->
        <transition name="fade">
            <div v-if="showInfoPopup" class="modal-overlay info-modal">
                <div class="modal-content info-content">
                    <h2>Welcome to Japanese NLP Parser!</h2>
                    <p>
                        This application allows you to parse Japanese sentences into morphemes,
                        view flashcards for vocabulary review, and take quizzes to improve your language skills.
                    </p>
                    <button class="close-info" @click="closeInfoPopup">Got it!</button>
                </div>
            </div>
        </transition>

        <!-- Header -->
        <header>
            <h1>Japanese NLP Parser</h1>
            <p class="text-center text-muted">by: Jabbar Ali Panggabean</p>
            <!-- Toggle Dark/Light Mode (icon-only, tanpa background) -->
            <button class="toggle-mode" @click="toggleDarkMode">
                <span v-if="isDarkMode">
                    <!-- Light icon (switch to light mode) -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFF"
                        class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 8a4 4 0 1 1 -3.995 4.2l-.005 -.2l.005 -.2a4 4 0 0 1 3.995 -3.8z" />
                        <path
                            d="M12 2a1 1 0 0 1 .993 .883l.007 .117v2a1 1 0 0 1 -1.993 .117l-.007 -.117v-2a1 1 0 0 1 1 -1z" />
                        <path
                            d="M17.693 4.893a1 1 0 0 1 1.497 1.32l-.083 .094l-1.4 1.4a1 1 0 0 1 -1.497 -1.32l.083 -.094l1.4 -1.4z" />
                        <path d="M21 11a1 1 0 0 1 .117 1.993l-.117 .007h-2a1 1 0 0 1 -.117 -1.993l.117 -.007h2z" />
                        <path
                            d="M16.293 16.293a1 1 0 0 1 1.32 -.083l.094 .083l1.4 1.4a1 1 0 0 1 -1.32 1.497l-.094 -.083l-1.4 -1.4a1 1 0 0 1 0 -1.414z" />
                        <path
                            d="M12 18a1 1 0 0 1 .993 .883l.007 .117v2a1 1 0 0 1 -1.993 .117l-.007 -.117v-2a1 1 0 0 1 1 -1z" />
                        <path
                            d="M6.293 16.293a1 1 0 0 1 1.497 1.32l-.083 .094l-1.4 1.4a1 1 0 0 1 -1.497 -1.32l.083 -.094l1.4 -1.4z" />
                        <path d="M6 11a1 1 0 0 1 .117 1.993l-.117 .007h-2a1 1 0 0 1 -.117 -1.993l.117 -.007h2z" />
                        <path
                            d="M4.893 4.893a1 1 0 0 1 1.32 -.083l.094 .083l1.4 1.4a1 1 0 0 1 -1.32 1.497l-.094 -.083l-1.4 -1.4a1 1 0 0 1 0 -1.414z" />
                    </svg>
                </span>
                <span v-else>
                    <!-- Dark icon (switch to dark mode) -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                        <path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2" />
                        <path d="M19 11h2m-1 -1v2" />
                    </svg>
                </span>
            </button>
        </header>

        <!-- Main Content -->
        <main>
            <!-- Form Parsing Section -->
            <section class="form-section">
                <form @submit.prevent="submitForm">
                    <label for="sentence">Enter a Japanese sentence:</label>
                    <textarea v-model="sentence" placeholder="日本語の文を入力してください。" rows="4"></textarea>
                    <button type="submit" :disabled="isLoading">
                        <span v-if="isLoading">
                            <span class="spinner"></span> Processing...
                        </span>
                        <span v-else>Parse</span>
                    </button>
                </form>
            </section>

            <!-- Sample Sentences Section -->
            <section class="samples-section">
                <p class="sample-text">Choose a sample sentence:</p>
                <div class="sample-buttons">
                    <button v-for="(sample, index) in sampleSentences" :key="index" @click="useSample(sample)">
                        {{ sample }}
                    </button>
                </div>
            </section>

            <!-- Parsing Result & Navigation -->
            <section v-if="morphemes.length" class="result-section">
                <h2>Parsing Result:</h2>
                <ul>
                    <li v-for="(morpheme, index) in morphemes" :key="index">
                        {{ morpheme.surface }} - {{ morpheme.part_of_speech }} ({{ morpheme.dictionary_form }})
                    </li>
                </ul>

                <div class="nav-buttons">
                    <button @click="handleFlashcardsClick" :class="{ active: showFlashcards }">
                        Flashcards
                    </button>
                    <button @click="openQuizModal" class="quiz-button">
                        Start Quiz
                    </button>
                </div>

                <!-- Flashcards (dengan transition dan scroll) -->
                <transition name="fade">
                    <div v-if="showFlashcards" ref="flashcardsSection">
                        <flashcards :morphemes="morphemes"></flashcards>
                    </div>
                </transition>
            </section>
        </main>

        <!-- Help Button: untuk memunculkan kembali popup info -->
        <button class="help-button" @click="openInfoPopup">
            ?
        </button>

        <!-- Quiz Modal -->
        <div v-if="quizModalVisible" class="modal-overlay">
            <div class="modal-content">
                <quiz-component :morphemes="morphemes" @close="closeQuizModal"></quiz-component>
            </div>
        </div>
    </div>
</template>

<script>
import Flashcards from "./Flashcards.vue";
import QuizComponent from "./QuizComponent.vue";

export default {
    components: {
        Flashcards,
        QuizComponent,
    },
    data() {
        return {
            sentence: "",
            morphemes: [],
            showFlashcards: false,
            quizModalVisible: false,
            isLoading: false,
            sampleSentences: [
                "今日は良い天気ですね。",
                "明日も頑張りましょう。",
                "日本語の勉強は楽しいです。",
            ],
            isDarkMode: false,
            showInfoPopup: true, // Muncul sekali saat pertama kali
        };
    },
    methods: {
        async submitForm() {
            this.isLoading = true;
            try {
                const response = await fetch("/parse", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({ sentence: this.sentence }),
                });
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                const data = await response.json();
                this.morphemes = data.morphemes;
            } catch (error) {
                console.error("Error:", error);
            } finally {
                this.isLoading = false;
            }
        },
        openQuizModal() {
            this.quizModalVisible = true;
        },
        closeQuizModal() {
            this.quizModalVisible = false;
        },
        useSample(sample) {
            this.sentence = sample;
        },
        handleFlashcardsClick() {
            this.toggleFlashcards();
            if (this.showFlashcards) {
                this.$nextTick(() => {
                    if (this.$refs.flashcardsSection) {
                        this.$refs.flashcardsSection.scrollIntoView({ behavior: "smooth" });
                    }
                });
            }
        },
        toggleFlashcards() {
            this.showFlashcards = !this.showFlashcards;
        },
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
        },
        closeInfoPopup() {
            this.showInfoPopup = false;
        },
        openInfoPopup() {
            this.showInfoPopup = true;
        },
    },
};
</script>

<style scoped>
/* Container menyatu dengan background body */
.app-container {
    width: 100%;
    margin: 0;
    padding: 20px;
    background-color: transparent;
    transition: background-color 0.3s, color 0.3s;
}

/* Header */
header {
    position: relative;
    text-align: center;
    margin-bottom: 20px;
}

h1 {
    margin: 0;
    color: #333333;
    transition: color 0.3s;
}

/* Dark mode: judul berubah menjadi putih */
.dark-mode h1 {
    color: #ffffff;
}

.text-center {
    text-align: center !important;
}

.text-muted {
    color: #6c757d !important;
}

/* Main Sections */
main {
    max-width: 800px;
    margin: 0 auto;
}

.form-section,
.samples-section,
.result-section {
    margin-bottom: 30px;
}

.samples-section .sample-text {
    font-size: 16px;
    color: #333;
    transition: color 0.3s;
}

.dark-mode .samples-section .sample-text {
    color: #ffffff;
}

/* Form */
form {
    margin-bottom: 20px;
}

textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 10px;
}

/* Tombol Umum */
button:not(.toggle-mode):not(.help-button) {
    padding: 10px 20px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

button:not(.toggle-mode):not(.help-button):hover:not(:disabled) {
    background-color: #0056b3;
    transform: translateY(-2px);
}

button:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}

/* Toggle Dark/Light Mode Button (icon-only, tanpa background) */
.toggle-mode {
    background: transparent;
    border: none;
    cursor: pointer;
    position: absolute;
    top: 20px;
    right: 20px;
    transition: color 0.3s;
}

.toggle-mode:hover {
    color: inherit;
}

/* Help Button (tetap kecil dan fixed di pojok bawah kanan) */
.help-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease;
}

.help-button:hover {
    background-color: #0056b3;
}

/* Loading Spinner */
.spinner {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid #ffffff;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
    display: inline-block;
    vertical-align: middle;
    margin-right: 5px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Navigation Menu */
.nav-buttons {
    display: flex;
    gap: 10px;
    margin: 20px 0;
    justify-content: center;
}

.quiz-button {
    background-color: #28a745;
}

.quiz-button:hover {
    background-color: #218838;
}

/* Sample Sentences */
.sample-sentences {
    margin-bottom: 20px;
    text-align: center;
}

.sample-sentences p {
    font-size: 16px;
    color: #333;
    margin-bottom: 10px;
    transition: color 0.3s;
}

.dark-mode .sample-sentences p {
    color: #ffffff;
}

.sample-buttons button {
    margin: 5px;
    padding: 8px 12px;
    background-color: #17a2b8 !important;
}

.sample-buttons button:hover {
    background-color: #138496 !important;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 100%;
    text-align: center;
}

.info-modal .modal-content {
    max-width: 600px;
}

.close-info {
    margin-top: 20px;
    padding: 8px 16px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.close-info:hover {
    background-color: #0056b3;
}

/* Transition Animation for Flashcards and Modal */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}

/* Dark Mode Styles */
.dark-mode {
    background-color: #2e2e2e;
    color: #ffffff;
}

.dark-mode textarea {
    background-color: #424242;
    color: #ffffff;
    border: 1px solid #555;
}

.dark-mode button:not(.toggle-mode):not(.help-button) {
    background-color: #4a90e2;
}

.dark-mode .modal-content {
    background-color: #424242;
    color: #ffffff;
}

/* Media Queries */
/* Tablet and Desktop */
@media (min-width: 768px) {
    .app-container {
        padding: 30px;
    }

    h1 {
        font-size: 2rem;
    }

    textarea {
        font-size: 18px;
        padding: 12px;
    }

    button:not(.toggle-mode):not(.help-button) {
        font-size: 18px;
        padding: 12px 24px;
    }

    .sample-sentences p {
        font-size: 18px;
    }

    .modal-content {
        max-width: 600px;
    }
}

/* Mobile */
@media (max-width: 767px) {
    h1 {
        font-size: 1.5rem;
    }

    textarea {
        font-size: 16px;
    }

    button:not(.toggle-mode):not(.help-button) {
        font-size: 16px;
    }
}
</style>
