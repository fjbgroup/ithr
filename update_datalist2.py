import os

filepath = 'resources/views/it/inventory/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

start_idx = content.find('<datalist id="assetTypeOptions">')
end_idx = content.find('</datalist>', start_idx)

if start_idx != -1 and end_idx != -1:
    new_datalist = """<datalist id="assetTypeOptions">
  @foreach($assetClasses as $cls)
  <option value="{{ $cls->name }}">
  @endforeach
</datalist>"""
    
    content = content[:start_idx] + new_datalist + content[end_idx + len('</datalist>'):]
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print('Updated datalist successfully.')
else:
    print('Could not find datalist in file.')
