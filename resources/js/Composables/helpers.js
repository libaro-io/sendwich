export function useHelpers() {
    function formatMoney(price) {
        return new Intl.NumberFormat('nl-BE', {style: 'currency', currency: 'EUR'}).format(price);
    }

    return {
        formatMoney,
    }
}
