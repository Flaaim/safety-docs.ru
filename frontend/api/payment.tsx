import {
  CreatePaymentDTO,
  PaymentInitResponse,
  PaymentResultDTO,
} from "@/interfaces/payment.interface";
import {API} from "@/app/api";
import {apiFetch} from "@api/apiClient";

export async function getPaymentByToken(token: string, signal?: AbortSignal): Promise<PaymentResultDTO> {
  return await apiFetch<PaymentResultDTO>(API.payment.getByToken(token), {
    method: 'GET',
    signal,
  });
}

export async function createPayment(payment: CreatePaymentDTO, signal?: AbortSignal): Promise<PaymentInitResponse> {
  return await apiFetch<PaymentInitResponse>(API.payment.create(), {
    method: 'POST',
    signal,
    body: JSON.stringify({
      email: payment.email,
      productId: payment.productId
    }),
  });
}
