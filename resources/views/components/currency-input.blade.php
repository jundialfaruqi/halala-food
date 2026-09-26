@props([
    'model',
    'placeholder' => '0',
    'class' => '',
    'size' => 'text-base',
    'disabled' => false,
])

<div x-data="{
    displayValue: '',
    rawAmount: @entangle($model).live,
    formatCurrency(val) {
        if (val === null || val === undefined || val === '') return '';
        let numStr = String(val).replace(/\D/g, '');
        if (!numStr) return '';
        return new Intl.NumberFormat('id-ID').format(numStr);
    },
    updateValue(e) {
        let digitsOnly = e.target.value.replace(/\D/g, '');
        let num = digitsOnly ? parseInt(digitsOnly, 10) : 0;
        this.rawAmount = num;
        this.displayValue = digitsOnly ? new Intl.NumberFormat('id-ID').format(digitsOnly) : '';
    },
    init() {
        if (this.rawAmount && this.rawAmount > 0) {
            this.displayValue = this.formatCurrency(this.rawAmount);
        }
        $watch('rawAmount', (val) => {
            if (val === null || val === undefined || val === '' || val == 0) {
                if (this.displayValue !== '') {
                    this.displayValue = '';
                }
            } else {
                let formatted = this.formatCurrency(val);
                if (this.displayValue !== formatted) {
                    this.displayValue = formatted;
                }
            }
        });
    }
}" class="w-full">
    <label class="input input-bordered flex items-center gap-2 w-full rounded-xl focus-within:border-slate-900 bg-white {{ $class }}">
        <span class="font-mono font-bold text-slate-400 text-base select-none shrink-0">Rp</span>
        <input 
            type="text" 
            inputmode="numeric"
            x-model="displayValue" 
            @input="updateValue($event)"
            placeholder="{{ $placeholder }}" 
            @if($disabled) disabled @endif
            class="grow font-mono font-bold {{ $size }} bg-transparent outline-none focus:outline-none border-none p-0 text-slate-900" 
            {{ $attributes->whereDoesntStartWith(['wire:model', 'class', 'size', 'placeholder', 'disabled', 'model']) }}
        />
    </label>
</div>
