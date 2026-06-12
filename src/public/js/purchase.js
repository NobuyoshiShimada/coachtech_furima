function updatePaymentDisplay() {
    const select = document.getElementById('payment-select');
    const display = document.getElementById('payment-display');
    const selectedText = select.options[select.selectedIndex].text;
    display.textContent = selectedText;
}
