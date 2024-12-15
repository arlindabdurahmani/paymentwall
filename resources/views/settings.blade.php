<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col">
            <i class="fa fa-theme-payments"></i>
        </td>
        <td style="width: 20%">
            <img class="filter-black" src="{{ asset('vendor/core/plugins/paymentwall/images/paymentwall.png') }}" alt="{{ $name }}">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://www.paymentwall.com/" target="_blank">{{ $name }}</a>
                    <p>{{ trans('plugins/paymentwall::paymentwall.description') }}</p>
                </li>
            </ul>
        </td>
    </tr>
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-start" style="margin-top: 5px;">
                <div @class(['payment-name-label-group', 'hidden' => ! $status])>
                    <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span>
                    <label class="ws-nm inline-display method-name-label">{{ get_payment_setting('name', $moduleName) }}</label>
                </div>
            </div>
            <div class="float-end">
                <a @class(['btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger', 'hidden' => ! $status])>{{ trans('plugins/payment::payment.edit') }}</a>
                <a @class(['btn btn-secondary toggle-payment-item save-payment-item-btn-trigger', 'hidden' => $status])>{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="payment-content-item hidden">
        <td colspan="3">
            <form>
                <input type="hidden" name="type" value="{{ $moduleName }}" class="payment_type">

                <div class="row">
                    <div class="col-sm-6">
                        <ul>
                            <li>
                                <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => $name]) }}</label>
                            </li>
                            <li class="payment-note">
                                <p>{{ trans('plugins/payment::payment.configuration_requirement', ['name' => $name]) }}:</p>
                                <ul class="m-md-l" style="list-style-type:decimal">
                                    <li style="list-style-type:decimal">
                                        <a href="https://www.paymentwall.com/register" target="_blank">
                                            {{ trans('plugins/payment::payment.service_registration', ['name' => $name]) }}
                                        </a>
                                    </li>
                                    <li style="list-style-type:decimal">
                                        <p>{{ trans('plugins/payment::payment.paymentwall_after_service_registration_msg', ['name' => $name]) }}</p>
                                    </li>
                                    <li style="list-style-type:decimal">
                                        <p>{{ trans('plugins/payment::payment.enter_project_and_secret_key') }}</p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <div class="well bg-white">
                            <x-core-setting::text-input
                                name="payment_paymentwall_name"
                                :label="trans('plugins/payment::payment.method_name')"
                                :value="get_payment_setting('name', $moduleName, trans('plugins/payment::payment.pay_online_via', ['name' => $name]))"
                                data-counter="400"
                            />

                            <x-core-setting::form-group>
                                <label class="text-title-field" for="payment_paymentwall_description">{{ trans('core/base::forms.description') }}</label>
                                <textarea class="next-input" name="payment_paymentwall_description" id="payment_paymentwall_description">{{ get_payment_setting('description', $moduleName, __('Pay securely with Paymentwall')) }}</textarea>
                            </x-core-setting::form-group>

                            <x-core-setting::text-input
                                :name="'payment_' . $moduleName . '_project_key'"
                                :label="trans('plugins/paymentwall::paymentwall.project_key')"
                                :value="get_payment_setting('project_key', $moduleName)"
                                placeholder="Enter your Project Key"
                            />

                            <x-core-setting::text-input
                                :name="'payment_' . $moduleName . '_secret_key'"
                                :label="trans('plugins/paymentwall::paymentwall.secret_key')"
                                :value="get_payment_setting('secret_key', $moduleName)"
                                type="password"
                                placeholder="Enter your Secret Key"
                            />

                            {!! apply_filters(PAYMENT_METHOD_SETTINGS_CONTENT, null, $moduleName) !!}
                        </div>
                    </div>
                </div>

                <div class="col-12 bg-white text-end">
                    <button @class(['btn btn-warning disable-payment-item', 'hidden' => ! $status]) type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                    <button @class(['btn btn-info save-payment-item btn-text-trigger-save', 'hidden' => $status]) type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                    <button @class(['btn btn-info save-payment-item btn-text-trigger-update', 'hidden' => ! $status]) type="button">{{ trans('plugins/payment::payment.update') }}</button>
                </div>
            </form>
        </td>
    </tr>
    </tbody>
</table>
