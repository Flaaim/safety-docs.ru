export interface PaymentResultDTO {
  status: "succeeded" | "pending" | "failed";
  email: string;
  returnToken: string;
  message?: string;
}

export interface PaymentInitResponse {
  returnUrl: string;
  amount?: number;
  currency?: string;
  status?: string;
}

export interface CreatePaymentDTO {
  email: string;
  documentId: string;
}
