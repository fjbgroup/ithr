import os

filepath = 'resources/views/it/masterdata/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace main tab name
content = content.replace('<i class="bi bi-tags-fill"></i> Asset Classes', '<i class="bi bi-tags-fill"></i> Asset Types / Classes')
content = content.replace('Manage asset classes, brands, and locations', 'Manage asset types, classes, brands, and locations')

# Replace IT specific section
it_section_start = content.find('{{-- IT Asset Classes --}}')
nit_section_start = content.find('{{-- Non-IT Asset Classes --}}')

if it_section_start != -1 and nit_section_start != -1:
    it_part = content[it_section_start:nit_section_start]
    it_part = it_part.replace('<h6>IT Asset Classes</h6>', '<h6>IT Asset Types</h6>')
    it_part = it_part.replace('<div class="ac-stat-lbl">Classes</div>', '<div class="ac-stat-lbl">Types</div>')
    it_part = it_part.replace('Add Class', 'Add Type')
    it_part = it_part.replace('No IT asset classes yet', 'No IT asset types yet')
    it_part = it_part.replace('Add a class above', 'Add a type above')
    it_part = it_part.replace('<th>Asset Class</th>', '<th>Asset Type</th>')
    it_part = it_part.replace('placeholder="New class name, e.g. MONITOR"', 'placeholder="New type name, e.g. MONITOR"')
    
    content = content[:it_section_start] + it_part + content[nit_section_start:]
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print('Updated masterdata index successfully.')
else:
    print('Could not find sections.')
