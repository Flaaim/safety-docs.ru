import {PaymentData, ProcessedPayment} from "@/interfaces/payment.interface";
import {API} from "@/app/api";
import {apiFetch} from "@api/apiClient";

export async function getPaymentByToken(token: string, signal?: AbortSignal): Promise<PaymentData> {
  return await apiFetch<PaymentData>(API.payment.getByToken(token), {
    method: 'GET',
    signal,
  })
}

export async function createPayment(email: string, productId: string, signal?: AbortSignal): Promise<ProcessedPayment> {
  return await apiFetch<ProcessedPayment>(API.payment.create(), {
    method: 'POST',
    signal,
    body: JSON.stringify({ email, productId }),
  })
}
