(function () {
	FormHelper = function () {
		this.init()
	};
	
	FormHelper.prototype.init = function() {
		console.log('test');
	};
	
	FormHelper.createString = function() {
		console.log('test string');
	}
	
	FormHelper.displayError = function(container, message) {
		container.prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + message + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
	}
})();

// TODO: WIDGET FACTORY - This is a first step
// Credits: from Kendo UI
function guid() {
	var id = '', i, random;

	for (i = 0; i < 32; i++) {
		random = Math.random() * 16 | 0;

		if (i == 8 || i == 12 || i == 16 || i == 20) {
			id += '-';
		}
		id += (i == 12 ? 4 : (i == 16 ? (random & 3 | 8) : random)).toString(16);
	}

	return id;
};




// TODO: FUTURE WIDGET TEMPLATING INJECT JS USING KENDO TEMPLATE SYNTAX
/*function compilePart(part, stringPart) {
	if (stringPart) {
		return "'" +
			part.split("'").join("\\'")
				.split('\\"').join('\\\\\\"')
				.replace(/\n/g, "\\n")
				.replace(/\r/g, "\\r")
				.replace(/\t/g, "\\t") + "'";
	} else {
		var first = part.charAt(0),
			rest = part.substring(1);

		if (first === "=") {
			return "+(" + rest + ")+";
		} else if (first === ":") {
			return "+$kendoHtmlEncode(" + rest + ")+";
		} else {
			return ";" + part + ";$kendoOutput+=";
		}
	}
}

var argumentNameRegExp = /^\w+/,
	encodeRegExp = /\$\{([^}]*)\}/g,
	escapedCurlyRegExp = /\\\}/g,
	curlyRegExp = /__CURLY__/g,
	escapedSharpRegExp = /\\#/g,
	sharpRegExp = /__SHARP__/g,
	zeros = ["", "0", "00", "000", "0000"];

Template = {
	paramName: "data", // name of the parameter of the generated template
	useWithBlock: true, // whether to wrap the template in a with() block
	render: function(template, data) {
		var idx,
			length,
			html = "";

		for (idx = 0, length = data.length; idx < length; idx++) {
			html += template(data[idx]);
		}

		return html;
	},
	compile: function(template, options) {
		var settings = $.extend({}, this, options),
			paramName = settings.paramName,
			argumentName = paramName.match(argumentNameRegExp)[0],
			useWithBlock = settings.useWithBlock,
			functionBody = "var $kendoOutput, $kendoHtmlEncode = kendo.htmlEncode;",
			fn,
			parts,
			idx;

		if (isFunction(template)) {
			return template;
		}

		functionBody += useWithBlock ? "with(" + paramName + "){" : "";

		functionBody += "$kendoOutput=";

		parts = template
			.replace(escapedCurlyRegExp, "__CURLY__")
			.replace(encodeRegExp, "#=$kendoHtmlEncode($1)#")
			.replace(curlyRegExp, "}")
			.replace(escapedSharpRegExp, "__SHARP__")
			.split("#");

		for (idx = 0; idx < parts.length; idx ++) {
			functionBody += compilePart(parts[idx], idx % 2 === 0);
		}

		functionBody += useWithBlock ? ";}" : ";";

		functionBody += "return $kendoOutput;";

		functionBody = functionBody.replace(sharpRegExp, "#");

		try {
			fn = new Function(argumentName, functionBody);
			console.log(fn);
			fn._slotCount = Math.floor(parts.length / 2);
			return fn;
		} catch(e) {
			throw new Error(template, functionBody);
		}

		console.log(parts);
		console.log(functionBody);
	}
};

template = $.proxy(Template.compile, Template);*/