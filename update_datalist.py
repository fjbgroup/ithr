import re

filepath = "resources/views/it/inventory/index.blade.php"

with open(filepath, "r", encoding="utf-8") as f:
    content = f.read()

# Define the datalist html
datalist_html = """
<datalist id="assetTypeOptions">
  <option value="Alarm system">
  <option value="Android">
  <option value="APC">
  <option value="Apple Mac">
  <option value="Appliance">
  <option value="Arduino">
  <option value="Automotive">
  <option value="AWS EC2 Instance">
  <option value="AWS EC2 VPC">
  <option value="Azure Resource Group">
  <option value="Azure Virtual Machine">
  <option value="Baby monitor">
  <option value="Badge reader">
  <option value="Balancer">
  <option value="Bar-code">
  <option value="Battery">
  <option value="Bell">
  <option value="BlackBerry">
  <option value="Blade server">
  <option value="Bridge">
  <option value="Cable modem">
  <option value="Camera">
  <option value="Car">
  <option value="Cell phone">
  <option value="Chrome OS">
  <option value="Chromecast">
  <option value="Circuit card">
  <option value="Citrix Guest">
  <option value="Citrix Pool">
  <option value="Citrix XenServer">
  <option value="Cleaner">
  <option value="Clock">
  <option value="Cloud">
  <option value="Communication">
  <option value="Computer">
  <option value="Conferencing">
  <option value="Control Panel">
  <option value="Database">
  <option value="Desktop">
  <option value="Device server">
  <option value="Disc Player">
  <option value="DNS server">
  <option value="Domain Server">
  <option value="Domotz Box">
  <option value="DSLAM device">
  <option value="DVD/Blu-Ray">
  <option value="Energy">
  <option value="Environment monitor">
  <option value="E-reader">
  <option value="ESXi server">
  <option value="External disk">
  <option value="Fax">
  <option value="Fibre switch">
  <option value="File Server">
  <option value="Fingbox">
  <option value="Firewall">
  <option value="Fitness">
  <option value="Fridge">
  <option value="FTP server">
  <option value="Game device">
  <option value="Garage">
  <option value="Gateway">
  <option value="Handheld">
  <option value="Health Monitor">
  <option value="Heating">
  <option value="Home automation">
  <option value="Host">
  <option value="Hub">
  <option value="Humidity">
  <option value="Hyper-V guest">
  <option value="Identity mgmt device">
  <option value="Industrial">
  <option value="Intrusion detection system">
  <option value="iOS">
  <option value="IP gateway">
  <option value="iPad">
  <option value="iPhone">
  <option value="iPod">
  <option value="Key Lock">
  <option value="KVM switch">
  <option value="Laptop">
  <option value="Light">
  <option value="Linux">
  <option value="Load balancer">
  <option value="Loudspeaker">
  <option value="Mail server">
  <option value="Management device">
  <option value="Media system">
  <option value="Medical">
  <option value="Memory stick">
  <option value="Microphone">
  <option value="Mobile">
  <option value="Monitor">
  <option value="Motion Detector">
  <option value="MSFC">
  <option value="Multiplexer">
  <option value="Music">
  <option value="Music system">
  <option value="NAS">
  <option value="Network Appliance">
  <option value="Network device">
  <option value="OT">
  <option value="Pet Monitor">
  <option value="Photos">
  <option value="Poe Plug">
  <option value="Pool">
  <option value="POS">
  <option value="Power distribution unit">
  <option value="Power injector">
  <option value="Power System">
  <option value="Printer">
  <option value="Probe">
  <option value="Processor">
  <option value="Projector">
  <option value="Proxy server">
  <option value="QOS device">
  <option value="Rack">
  <option value="Radio">
  <option value="Raspberry">
  <option value="Remote Access Controller">
  <option value="Remote Control">
  <option value="RFID">
  <option value="Robot">
  <option value="Router">
  <option value="RSFC">
  <option value="RSM">
  <option value="SAN">
  <option value="Satellite">
  <option value="Scale">
  <option value="Scanner">
  <option value="Security appliance">
  <option value="Security system">
  <option value="Sensor">
  <option value="Server">
  <option value="Sleep">
  <option value="Small Cell">
  <option value="Smart Controller">
  <option value="Smart Home">
  <option value="Smart Meter">
  <option value="Smart Plug">
  <option value="Smart TV">
  <option value="Smoke">
  <option value="Solar Panel">
  <option value="Sound System">
  <option value="Sprinkler">
  <option value="SSL/VPN device">
  <option value="STB">
  <option value="Streaming Dongle">
  <option value="Surveillance Camera">
  <option value="Switch">
  <option value="Tablet">
  <option value="Tape device">
  <option value="Telephone system">
  <option value="Terminal">
  <option value="Terminal server">
  <option value="Thermostat">
  <option value="Toy">
  <option value="Unix">
  <option value="Unknown">
  <option value="UPS">
  <option value="USB">
  <option value="Video device">
  <option value="Virtual Machine">
  <option value="VMware Guest">
  <option value="VMware vCenter server">
  <option value="Voice Control">
  <option value="VOIP Gateway">
  <option value="VOIP phone">
  <option value="VPN device">
  <option value="Washer">
  <option value="Watch">
  <option value="Wearable">
  <option value="Weather">
  <option value="Webserver">
  <option value="Wifi">
  <option value="Wifi Extender">
  <option value="Windows">
  <option value="Windows CE">
  <option value="Windows Phone">
  <option value="Wireless Access point">
</datalist>
"""

# Insert the datalist right before the end section
if "assetTypeOptions" not in content:
    end_section = content.rfind('@endsection')
    if end_section != -1:
        content = content[:end_section] + datalist_html + "\n" + content[end_section:]

# We need to add list="assetTypeOptions" to the asset_type inputs
# 1. Edit modal
old_edit_input = '<input type="text" name="asset_type" id="ief_asset_type" required>'
new_edit_input = '<input type="text" name="asset_type" id="ief_asset_type" list="assetTypeOptions" autocomplete="off" required>'
content = content.replace(old_edit_input, new_edit_input)

# 2. Add modal (the input there looks like this based on python script from before):
old_add_input = '<input type="text" name="asset_type" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:\'Inter\',sans-serif;outline:none;box-sizing:border-box" required>'
new_add_input = '<input type="text" name="asset_type" list="assetTypeOptions" autocomplete="off" style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:\'Inter\',sans-serif;outline:none;box-sizing:border-box" required>'
content = content.replace(old_add_input, new_add_input)

with open(filepath, "w", encoding="utf-8") as f:
    f.write(content)
print("Forms updated with datalist")
