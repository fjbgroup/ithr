import os

filepath = 'resources/views/welcome.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

replacements = {
    'Â·': '·',
    'âš ï¸ ': '⚠️',
    'â€¦': '…',
    'âœ•': '✕',
    'â† ': '←',
    'Ã—': '×'
}

for k, v in replacements.items():
    content = content.replace(k, v)

with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print("Mojibake phase 2 fixed via Python.")
