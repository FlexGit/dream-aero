<form id="pay_form" method="post" action="{{ $url }}">
	<input type="hidden" name="MNT_ID" value="{{ $MNT_ID }}">
	<input type="hidden" name="MNT_AMOUNT" value="{{ $MNT_AMOUNT }}">
	<input type="hidden" name="MNT_TRANSACTION_ID" value="{{ $MNT_TRANSACTION_ID }}">
	<input type="hidden" name="MNT_CURRENCY_CODE" value="{{ $MNT_CURRENCY_CODE }}">
	<input type="hidden" name="MNT_TEST_MODE" value="{{ $MNT_TEST_MODE }}">
	<input type="hidden" name="MNT_DESCRIPTION" value="{{ $MNT_DESCRIPTION }}">
	<input type="hidden" name="MNT_SUBSCRIBER_ID" value="{{ $MNT_SUBSCRIBER_ID }}">
	<input type="hidden" name="MNT_SIGNATURE" value="{{ $MNT_SIGNATURE }}">
	<input type="hidden" name="MNT_SUCCESS_URL" value="{{ $MNT_SUCCESS_URL }}">
	<input type="hidden" name="MNT_FAIL_URL" value="{{ $MNT_FAIL_URL }}">
	<input type="hidden" name="MNT_RETURN_URL" value="{{ $MNT_RETURN_URL }}">
</form>