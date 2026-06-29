import os

directory = 'public/assets'

replacements = {
    'â†’': '→',
    'â† ': '←',
    'â†‘': '↑',
    'â†“': '↓',
    'â•‘': '║',
    'âœ¦': '✦',
    'â€¦': '…',
    'Ã—': '×',
    'â€”': '—',
    'â€“': '-',
    'â Œ': '❌',
    'âš ï¸ ': '⚠️',
    'â˜€ï¸ ': '☀️',
    'âœ“': '✓',
    'ðŸš«': '🚫',
    'ðŸ‘¥': '👥',
    'ðŸ”‘': '🔑',
    'ðŸ—“ï¸ ': '🗓️',
    'ðŸ“…': '📅',
    'âœ¨': '✨',
    'ðŸ›‹ï¸ ': '🛋️',
    'ðŸ” ': '🔧',
    'ðŸŽ“': '🎓',
    'ðŸŒŠ': '🌊',
    'â­ ': '⭐',
    'ðŸ ¢': '🏢',
    'ðŸ’¡': '💡',
    'ðŸ“‹': '📋',
    'Â·': '·',
    'Ã¢â€ â€™': '→',
    'â”€â”€': '──',
    'â†”': '↔'
}

count = 0

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith('.css') or file.endswith('.js'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                try:
                    content = f.read()
                except UnicodeDecodeError:
                    continue
            
            original_content = content
            for k, v in replacements.items():
                content = content.replace(k, v)
            
            if content != original_content:
                with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
                    f.write(content)
                count += 1
                print(f"Fixed {filepath}")

print(f"Fixed {count} files.")
