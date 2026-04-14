export function validatePayments(lstPays, infoAmounts) {
    // Ignorar los que están en 0 (mixto los pre-llena todos)
    const activePays = lstPays.filter(i => Number(i.amount) > 0);

    // 1. Que haya al menos un método con monto real
    if (activePays.length === 0) {
        toastr.error('Ingrese al menos un monto de pago.');
        return { ok: false };
    }

    // 2. Que el total de los activos cubra el pedido
    console.log('infoAmounts',infoAmounts);
    const totalPaid = activePays.reduce((sum, i) => sum + Number(i.amount), 0);
    if (totalPaid < infoAmounts.total) {
        toastr.error(`Falta cubrir S/ ${(infoAmounts.total - totalPaid).toFixed(2)} del total.`);
        return { ok: false };
    }

    return { ok: true };
}
