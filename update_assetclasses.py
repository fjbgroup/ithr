import os

filepath = 'resources/views/it/asset-classes/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace general headers
content = content.replace('>Asset Classes</span>', '>Asset Types / Classes</span>')
content = content.replace('<h4>Asset Classes</h4>', '<h4>Asset Types / Classes</h4>')
content = content.replace('Manage asset classes for IT and Non-IT assets', 'Manage asset types and classes for IT and Non-IT assets')
content = content.replace('Asset Classes</h4>', 'Asset Types / Classes</h4>')

# Replace IT specific section
it_section_start = content.find('<!-- ══ IT ASSET CLASSES ══ -->')
nit_section_start = content.find('<!-- ══ NON-IT ASSET CLASSES ══ -->')

if it_section_start != -1 and nit_section_start != -1:
    it_part = content[it_section_start:nit_section_start]
    it_part = it_part.replace('<h6>IT Asset Classes</h6>', '<h6>IT Asset Types</h6>')
    it_part = it_part.replace('<div class="ac-stat-lbl">Classes</div>', '<div class="ac-stat-lbl">Types</div>')
    it_part = it_part.replace('Add Class', 'Add Type')
    it_part = it_part.replace('No IT asset classes yet', 'No IT asset types yet')
    it_part = it_part.replace('Add a class above', 'Add a type above')
    it_part = it_part.replace('<th>Asset Class</th>', '<th>Asset Type</th>')
    it_part = it_part.replace('placeholder="New class name', 'placeholder="New type name')
    
    content = content[:it_section_start] + it_part + content[nit_section_start:]
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print('Updated asset-classes index successfully.')
else:
    print('Could not find sections.')
