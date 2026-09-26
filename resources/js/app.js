import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import Chart from 'chart.js/auto';

flatpickr.localize(Indonesian);
window.flatpickr = flatpickr;
window.Chart = Chart;