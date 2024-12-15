<div class="payment-settings">
    <h3>{{ trans('plugins/paymentwall::paymentwall.settings_title') }}</h3>
    <p>{{ trans('plugins/paymentwall::paymentwall.settings_description') }}</p>
    <form method="POST" action="{{ route('admin.settings.paymentwall.save') }}">
        @csrf
        <div class="form-group">
            <label for="public_key">Public Key</label>
            <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $settings['public_key'] ?? '') }}">
        </div>
        <div class="form-group">
            <label for="secret_key">Secret Key</label>
            <input type="text" name="secret_key" class="form-control" value="{{ old('secret_key', $settings['secret_key'] ?? '') }}">
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
