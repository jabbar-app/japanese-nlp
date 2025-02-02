<template>
    <div class="quiz-container">
        <h2>Quiz Mode</h2>

        <!-- Header: Timer & Progress -->
        <div v-if="currentQuestionIndex < questions.length" class="quiz-header">
            <div class="timer">
                <p><strong>Time Left:</strong> {{ timer }} seconds</p>
            </div>
            <div class="question-counter">
                <p>Question {{ currentQuestionIndex + 1 }} of {{ questions.length }}</p>
            </div>
        </div>

        <!-- Quiz Content -->
        <div v-if="currentQuestionIndex < questions.length" class="quiz-content">
            <p class="question-text">{{ currentQuestion.text }}</p>
            <div class="options">
                <button v-for="(option, index) in currentQuestion.options" :key="index" @click="checkAnswer(option)"
                    :class="getButtonClass(option)" :disabled="answered">
                    {{ option }}
                </button>
            </div>
        </div>

        <!-- Quiz Completed -->
        <div v-else class="quiz-completed">
            <h3>Quiz Completed!</h3>
            <p>Your score: {{ score }} / {{ questions.length }}</p>
            <div class="quiz-actions">
                <button @click="restartQuiz" class="restart-button">Restart Quiz</button>
                <button @click="$emit('close')" class="close-button">Close Quiz</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        morphemes: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            questions: [],
            currentQuestionIndex: 0,
            timer: 10,
            timerInterval: null,
            score: 0,
            answered: false,
        };
    },
    computed: {
        currentQuestion() {
            return this.questions[this.currentQuestionIndex];
        },
    },
    created() {
        this.generateQuestions();
        this.startTimer();
    },
    methods: {
        generateQuestions() {
            const questionTypes = ['dictionary_form', 'reading_form', 'part_of_speech'];
            this.questions = this.morphemes.map((morpheme) => {
                const randomType = questionTypes[Math.floor(Math.random() * questionTypes.length)];
                const questionText = this.getQuestionText(morpheme, randomType);
                const correctAnswer = morpheme[randomType];
                const options = this.generateOptions(correctAnswer, randomType);
                return {
                    text: questionText,
                    correctAnswer,
                    options,
                };
            });
            this.questions = this.shuffleArray(this.questions);
        },
        getQuestionText(morpheme, type) {
            switch (type) {
                case 'dictionary_form':
                    return `What is the dictionary form of: ${morpheme.surface}?`;
                case 'reading_form':
                    return `What is the reading form of: ${morpheme.surface}?`;
                case 'part_of_speech':
                    return `What is the part of speech of: ${morpheme.surface}?`;
                default:
                    return '';
            }
        },
        generateOptions(correctAnswer, type) {
            const allAnswers = this.morphemes.map((morpheme) => morpheme[type]);
            const uniqueAnswers = [...new Set(allAnswers)];
            const shuffledAnswers = this.shuffleArray(uniqueAnswers);
            const options = shuffledAnswers.slice(0, 3);
            if (!options.includes(correctAnswer)) {
                options.push(correctAnswer);
            }
            return this.shuffleArray(options);
        },
        shuffleArray(array) {
            return array.sort(() => Math.random() - 0.5);
        },
        checkAnswer(option) {
            this.answered = true;
            if (option === this.currentQuestion.correctAnswer) {
                this.score++;
            }
            setTimeout(() => {
                this.nextQuestion();
            }, 1000);
        },
        getButtonClass(option) {
            if (!this.answered) return '';
            return option === this.currentQuestion.correctAnswer ? 'correct' : 'wrong';
        },
        nextQuestion() {
            this.answered = false;
            this.currentQuestionIndex++;
            this.resetTimer();
        },
        restartQuiz() {
            this.currentQuestionIndex = 0;
            this.score = 0;
            this.generateQuestions();
            this.resetTimer();
        },
        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    this.nextQuestion();
                }
            }, 1000);
        },
        resetTimer() {
            clearInterval(this.timerInterval);
            this.timer = 10;
            this.startTimer();
        },
    },
    beforeDestroy() {
        clearInterval(this.timerInterval);
    },
};
</script>

<style scoped>
/* Tetap gunakan tema light untuk quiz */
.quiz-container {
    width: 95%;
    max-width: 1000px;
    margin: 20px auto;
    padding: 30px;
    background-color: #f8f9fa !important;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: center;
    transition: background-color 0.3s, color 0.3s;
    /* Paksa warna teks tetap gelap */
    color: #333333 !important;
}

/* Header with Timer & Counter */
.quiz-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.timer,
.question-counter {
    font-size: 18px;
    color: #333333 !important;
}

/* Question Text */
.question-text {
    font-size: 20px;
    margin: 10px 0;
    color: #333333 !important;
}

/* Options */
.options {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    margin-top: 15px;
}

.options button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.options button:hover:not(:disabled) {
    background-color: #0056b3;
}

.options button:disabled {
    cursor: not-allowed;
}

/* Feedback colors */
button.correct {
    background-color: #28a745;
    color: #fff;
}

button.correct:hover {
    background-color: #218838;
}

button.wrong {
    background-color: #dc3545;
    color: #fff;
}

/* Quiz Completed */
.quiz-completed h3 {
    font-size: 24px;
    margin-bottom: 15px;
}

.quiz-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.quiz-actions button {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.restart-button {
    background-color: #17a2b8;
    color: #fff;
}

.restart-button:hover {
    background-color: #138496;
}

.close-button {
    background-color: #dc3545;
    color: #fff;
}

.close-button:hover {
    background-color: #c82333;
}

/* Media Query untuk layar besar */
@media (min-width: 768px) {
    .quiz-container {
        padding: 40px;
        font-size: 18px;
    }

    .options button {
        font-size: 18px;
        padding: 12px 24px;
    }
}
</style>
