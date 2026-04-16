export interface PaymentResultDTO {
  status: 'succeeded' | 'pending' | 'failed'
  email: string
  returnToken: string
  message?: string
}

export interface PaymentInitResponse {
  returnUrl: string
}

export interface CreatePaymentDTO {
  email: string,
  productId: string
}
