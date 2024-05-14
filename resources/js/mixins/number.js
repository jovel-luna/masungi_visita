export default {
	methods: {
		round(value) {
			return Math.round(value);
		},

		toFloat(value) {
			return parseFloat(value);
		},

		toMoney(value, prefix = '₱') {
			if (!value) { return; }
			return prefix + ' ' + (parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')).toString();
		},

		withComma(value) {
            return parseFloat(value).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        isNumeric(str) {
		  return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
		         !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
		},

		getMinMax(value, min, max) {
			if(parseInt(value) < min || isNaN(parseInt(value)))
                return min;
            else if(parseInt(value) > max)
                return max;
            else return value;
		}
	}
}