<template>
    <div class="quiz-container">
        <h2>Quiz Mode</h2>

        <!-- Timer -->
        <div v-if="currentQuestionIndex < questions.length">
            <p><strong>Time Left:</strong> {{ timer }} seconds</p>

            <!-- Question -->
            <p><strong>Question {{ currentQuestionIndex + 1 }}:</strong></p>
            <p>{{ currentQuestion.text }}</p>

            <!-- Multiple Choice Options -->
            <div class="options">
                <button v-for="(option, index) in currentQuestion.options" :key="index" @click="checkAnswer(option)"
                    :class="getButtonClass(option)" :disabled="answered">
                    {{ option }}
                </button>
            </div>
        </div>

        <!-- Quiz Completed -->
        <div v-else>
            <h3>Quiz Completed!</h3>
            <p>Your score: {{ score }}/{{ questions.length }}</p>

            <!-- Restart and Close Buttons -->
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

            if (option === this.currentQuestion.correctAnswer) {
                return 'correct';
            } else {
                return 'wrong';
            }
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
            this.startTimer();
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
.quiz-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

p {
    font-size: 18px;
}

.options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin: 5px;
}

button:hover {
    background-color: #0056b3;
}

button.correct {
    background-color: #28a745;
    color: white;
}

button.correct:hover {
    background-color: #218838;
}

/* button.wrong {
    background-color: #dc3545;
    color: white;
}

button.wrong:hover {
    background-color: #c82333;
} */

.quiz-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.restart-button {
    background-color: #17a2b8;
}

.restart-button:hover {
    background-color: #138496;
}

.close-button {
    background-color: #dc3545;
}

.close-button:hover {
    background-color: #c82333;
}
</style>
