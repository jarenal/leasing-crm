requirejs.config({
	baseUrl: "/js/lib",
	waitSeconds: 0,
	paths: {
		app: "../app",
		jquery: "jquery.min",
		jquery_ui: "jquery-ui",
		knockout: "knockout-3.3.0.debug",
		router: "../../bundles/fosjsrouting/js/router",
		fos_js_routes: "fos_js_routes",
		underscore: 'underscore-min',
		dust: "dust-full.min",
		compiled: "../compiled",
		bootstrap_table: "bootstrap-table.min",
		checkbox: "checkbox-x",
		fileinput: "fileinput",
		jquery_scrollto: "jquery.scrollTo.min"
	},
	shim: {
		"knockout": {
			exports: "ko"
		},
		"dust": {
			exports: "dust"
		},
		"compiled/common": {
			deps: ["dust"],
		},
		"compiled/contacts": {
			deps: ["dust"],
		},
		"compiled/properties": {
			deps: ["dust"],
		},
		"compiled/organisations": {
			deps: ["dust"],
		},
		"compiled/tickets": {
			deps: ["dust","jquery_ui"],
		},
		"compiled/leaseagreement": {
			deps: ["dust"],
		},
		"compiled/tenancies": {
			deps: ["dust"],
		},
		"compiled/breakdown": {
			deps: ["dust"],
		},
		"jquery": {
			exports: "$"
		},
		"bootstrap": {
			deps: ["jquery"]
		},
		"router": {
			exports: "fos"
		},
		"fos_js_routes": {
			deps: ["router"]
		},
		"jquery.combobox": {
			deps: ["jquery_ui","jquery"]
		},
		"underscore": {
			deps:["jquery"],
			exports: "_"
		},
		"checkbox": {
			deps: ["jquery"]
		},
		"fileinput": {
			deps: ["jquery"]
		},
		"bootstrap_table": {
			deps: ["jquery"]
		},
		"jquery.chktree": {
			deps: ["jquery"]
		},
		"jquery.smartcombo": {
			deps: ["jquery.combobox"]
		},
		"jquery.multifile": {
			deps: ["jquery"]
		},
		"jquery.multi-tenant": {
			deps: ["jquery"]
		},
		"knockout-app-validation": {
			deps: ["knockout"]
		},
		"domReady": {
			deps: ["fos_js_routes"]
		},
		"jquery_scrollto": {
			deps: ["jquery"]
		}
	},
	map: {
		"*": { "dust.core": "dust"}
	}

});