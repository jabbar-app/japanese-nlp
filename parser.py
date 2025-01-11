#!/usr/bin/env python3

from sudachipy import dictionary
import json
import sys

# Pastikan output terminal menggunakan encoding UTF-8
sys.stdout.reconfigure(encoding='utf-8')

def parse(sentence):
    tokenizer_obj = dictionary.Dictionary().create()
    mode = tokenizer_obj.SplitMode.C

    result = []
    for morpheme in tokenizer_obj.tokenize(sentence, mode):
        result.append({
            'surface': morpheme.surface(),
            'part_of_speech': ', '.join(morpheme.part_of_speech()),
            'dictionary_form': morpheme.dictionary_form(),
            'reading_form': morpheme.reading_form(),
            'normalized_form': morpheme.normalized_form()
        })

    return json.dumps(result, ensure_ascii=False)

if __name__ == '__main__':
    if len(sys.argv) > 1:
        sentence = sys.argv[1]
        print(parse(sentence))
    else:
        print("Please provide a sentence to parse.")
