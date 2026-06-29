import os

filepath = 'resources/views/welcome.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

replacements = {
    'ðŸ‘¥': '👥',
    'ðŸ”‘': '🔑',
    'ðŸ—“ï¸ ': '🗓️',
    'ðŸ“…': '📅',
    'â†’': '→',
    'â€”': '—',
    'â€“': '-',
    'Ã—': '×',
    'âš ï¸ ': '⚠️',
    'â˜€ï¸ ': '☀️',
    'âœ“': '✓',
    'â† ': '←',
    'âœ¨': '✨',
    'â Œ': '❌'
}

for k, v in replacements.items():
    content = content.replace(k, v)

with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print("Mojibake fixed via Python.")
