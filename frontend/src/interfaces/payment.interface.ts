

export interface PaymentData {
  status: 'succeeded' | 'pending' | 'failed'
  email: string
  returnToken: string
  message: string
}

export interface ProcessedPayment {
  amount: string
  currency: string,
  status: string,
  returnUrl: string
}
