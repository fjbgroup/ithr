import re
filepath = 'resources/views/it/inventory/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove literal \n from the HTML sections
content = content.replace('\\n', '') # wait, this will remove \n from my regex fixes?
