

export interface PaymentData {
  status: 'succeeded' | 'pending' | 'failed'
  email: string
  returnToken: string
  message: string
}
