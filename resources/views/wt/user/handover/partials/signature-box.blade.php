<div class="signature-box" data-signature-box data-saved-signature-url="{{ $savedSignatureUrl ?: '' }}">
    <div class="signature-head">
        <div class="signature-head-title">{{ $title }}</div>
        <button type="button" class="signature-clear" data-signature-clear>Clear</button>
    </div>
    <div style="padding:10px 12px;border-bottom:1px solid var(--border)">
        <div class="pickup-label">Signer Name</div>
        <input type="text" name="{{ $nameField }}" value="{{ old($nameField) }}" placeholder="Type name here..." class="signature-name" required>
    </div>
    <div class="signature-source">
        <label><input type="radio" name="{{ $sourceField }}" value="draw" data-signature-source checked> Draw</label>
        @if($savedSignatureUrl)
        <label><input type="radio" name="{{ $sourceField }}" value="saved" data-signature-source> Profile</label>
        @endif
        <label><input type="radio" name="{{ $sourceField }}" value="upload" data-signature-source> Upload</label>
    </div>
    <div class="signature-upload" data-upload-panel>
        <input type="file" accept="image/*" data-signature-upload class="form-control">
    </div>
    <div class="signature-pad" data-signature-pad>
        <canvas aria-label="{{ $title }}"></canvas>
    </div>
    <div class="signature-preview" data-signature-preview>
        <img alt="{{ $title }} preview">
    </div>
    <input type="hidden" name="{{ $signatureField }}" data-signature-input>
</div>
