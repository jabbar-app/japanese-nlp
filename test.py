from sudachipy import dictionary

tokenizer_obj = dictionary.Dictionary().create()
sentence = "私は日本に行きます。"
mode = tokenizer_obj.SplitMode.C

for morpheme in tokenizer_obj.tokenize(sentence, mode):
    print(morpheme.surface(), morpheme.part_of_speech())
