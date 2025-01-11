<template>
  <div class="app-container">
    <h1>Japanese NLP Parser</h1>

    <!-- Form Parsing -->
    <form @submit.prevent="submitForm">
      <label for="sentence">Enter a Japanese sentence:</label>
      <textarea v-model="sentence" placeholder="Masukkan kalimat Jepang" rows="4"></textarea>
      <button type="submit">Parse</button>
    </form>

    <!-- Feedback Parsing -->
    <div v-if="morphemes.length">
      <h2>Parsing Result:</h2>
      <ul>
        <li v-for="(morpheme, index) in morphemes" :key="index">
          {{ morpheme.surface }} - {{ morpheme.part_of_speech }} ({{ morpheme.dictionary_form }})
        </li>
      </ul>

      <!-- Navigation Menu -->
      <div class="nav-buttons">
        <button @click="showFlashcards = true; showQuiz = false" :class="{ active: showFlashcards }">
          Flashcards
        </button>
        <button @click="openQuizModal" class="quiz-button">
          Start Quiz
        </button>
      </div>

      <!-- Flashcards -->
      <flashcards v-if="showFlashcards" :morphemes="morphemes"></flashcards>
    </div>

    <!-- Quiz Modal -->
    <div v-if="quizModalVisible" class="modal-overlay">
      <div class="modal-content">
        <quiz-component v-if="quizModalVisible" :morphemes="morphemes" @close="closeQuizModal"></quiz-component>
      </div>
    </div>
  </div>
</template>

<script>
import Flashcards from './Flashcards.vue';
import QuizComponent from './QuizComponent.vue';

export default {
  components: {
    Flashcards,
    QuizComponent,
  },
  data() {
    return {
      sentence: '',
      morphemes: [],
      showFlashcards: true,
      quizModalVisible: false,
    };
  },
  methods: {
    async submitForm() {
      try {
        const response = await fetch('/parse', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({ sentence: this.sentence }),
        });

        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const data = await response.json();
        this.morphemes = data.morphemes;
      } catch (error) {
        console.error('Error:', error);
      }
    },
    openQuizModal() {
      this.quizModalVisible = true;
    },
    closeQuizModal() {
      this.quizModalVisible = false;
    },
  },
};
</script>

<style scoped>
/* Styling Utama */
.app-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #333333;
  margin-bottom: 20px;
}

form {
  margin-bottom: 20px;
}

textarea {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 10px;
}

button {
  padding: 10px 20px;
  background-color: #007bff;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: #0056b3;
}

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

/* Modal Styling */
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
}

.modal-content {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 100%;
}
</style>
