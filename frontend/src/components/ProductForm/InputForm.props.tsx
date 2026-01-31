import {DetailedHTMLProps, HTMLAttributes} from "react";


export interface InputFormProps extends DetailedHTMLProps<HTMLAttributes<HTMLInputElement>, HTMLInputElement> {
  type: 'email' | 'hidden'
  name: 'email' | 'productId'
  placeholder?: string
  value: string
  id: string
}
