<link href="{{ asset('css/styles.min.css?v=3') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/default.css?v=11') }}" rel="stylesheet" type="text/css" />

<style type="text/css">
	.modal-header {
		border-bottom: 0;
	}
	.modal-content {
		border: 3px solid #FF8200;
		border-radius: 0;
	}
	.modal-dialog {
		-webkit-transform: translate(0,0);
		transform: translate(0,0);
		transition: transform .3s ease-out,
		-webkit-transform .3s ease-out;
		width: 450px;
	}
	.gl-default, .gl-change-select {
		width: inherit;
		padding-top: 0;
	}
	.dropdown-menu {
		top: auto;
		left: auto;
	}
	.grecaptcha-badge {
		visibility: hidden !important;
	}
	.nice-select {
		height: 28px;
		color: #828285 !important;;
		padding-left: 8px;
		padding-right: 20px;
		/*overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;*/
		line-height: 28px;
		border: 0 !important;
		border-radius: 0 !important;
		border-bottom: 2px solid #828285 !important;
	}
	.popup-description {
		margin-top: 0;
	}
	/*.col-md-6 {
		padding-left: 0;
		padding-right: 0;
	}*/
	.consent-container {
		margin-top: 20px;
	}
	.promocode_container {
		margin-top: 20px;
		margin-left: 18px;
		margin-right: 18px;
	}
	.aeroflot_container {
		margin-top: 10px;
		margin-left: 18px;
		margin-right: 18px;
	}
	.aeroflot-buttons-container {
		margin-top: 20px;
		text-align: center;
	}
	.aeroflot-card-verified {
		background-color: rgb(170, 221, 199);
	}
	#bonus_info input {
		border: 0;
		margin-bottom: 24px;
	}
	.cont {
		margin-bottom: 7px;
		padding-top: 3px;
	}
	.popup .nice-select {
		padding-left: 10px;
	}
	.popup .popup-small-button {
		margin: 1px 0 0 5px;
		padding: 0;
		width: 90px;
		height: 24px;
		font-size: 12px;
	}
	@media screen and (max-width: 767px) {
		.popup {
			padding-left: 15px !important;
			padding-right: 15px !important;;
		}
	}
	.text-error {
		color: red;
	}
	.text-success {
		color: green;
	}
	.text-small {
		font-size: 12px;
	}
	.button-pipaluk-grey:after {
		background-color: #bbb !important;
	}
	.button-pipaluk-unactive:after {
		background-color: #bbb !important;
	}
	.button-pipaluk-unactive:before {
		border-color: #bbb !important;
	}
	.close-btn {
		margin: 3px 0 0 2px;
		cursor: pointer;
	}
	.pl-10 {
		padding-left: 10px;
	}
	.pr-10 {
		padding-right: 10px;
	}
	.pt-3 {
		padding-top: 3px;
	}
</style>
