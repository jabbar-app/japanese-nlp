<template>
    <div v-if="morphemes.length" class="flashcards-container">
        <h2>Flashcards</h2>
        <div class="flashcards">
            <div class="card-container" v-for="(morpheme, index) in morphemes" :key="index">
                <div class="card" :class="{ flipped: flippedCards[index] }" @click="flipCard(index)">
                    <!-- Bagian Depan Kartu -->
                    <div class="front">
                        <h3>{{ morpheme.surface }}</h3>
                    </div>
                    <!-- Bagian Belakang Kartu -->
                    <div class="back">
                        <p><strong>Dictionary Form:</strong> {{ morpheme.dictionary_form }}</p>
                        <p><strong>Reading Form:</strong> {{ morpheme.reading_form }}</p>
                        <p><strong>Part of Speech:</strong> {{ morpheme.part_of_speech }}</p>
                    </div>
                </div>
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
            flippedCards: [],
        };
    },
    created() {
        this.flippedCards = Array(this.morphemes.length).fill(false);
    },
    methods: {
        flipCard(index) {
            this.flippedCards[index] = !this.flippedCards[index];
        },
    },
};
</script>

<style scoped>
.flashcards-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.flashcards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.card-container {
    width: 180px;
    height: 240px;
    perspective: 1000px;
}

.card {
    width: 100%;
    height: 100%;
    position: relative;
    transition: transform 0.6s ease-in-out;
    transform-style: preserve-3d;
    cursor: pointer;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
}

.card:hover {
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  transform: scale(1.02);
}

.card.flipped {
    transform: rotateY(180deg);
}

.front,
.back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
    border-radius: 12px;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
}

.front {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
    font-size: 24px;
    text-align: center;
}

.back {
    background-color: #f8f9fa;
    color: #333333;
    transform: rotateY(180deg);
    font-size: 18px;
}

.back p {
    margin: 8px 0;
    text-align: center;
}

h2 {
    color: #333333;
    margin-bottom: 20px;
}
</style>
