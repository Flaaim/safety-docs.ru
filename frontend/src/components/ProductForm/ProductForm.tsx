'use client'

import {z} from 'zod'
import {FormEvent, JSX, useState} from "react";
import {ProductFormProps} from "./ProductForm.props";
import styles from './ProductForm.module.css'
import cn from 'classnames'
import {InputFormProps} from "./InputForm.props";
import {LabelFormProps} from "./LabelForm.props";
import {ButtonProps} from "./Button.props";
import {Status} from "@/components/Status/Status";

const schema = z.object({
  email: z.string().email('Некорректный email адрес'),
  productId: z.string().uuid('Некорректный ID продукта')
})

export const InputForm = ({type, name, value, id, placeholder}: InputFormProps): JSX.Element => {
  return <>
    <input
      name={name}
      type={type}
      defaultValue={value}
      placeholder={placeholder}
      id={id}
    />
  </>
}

export const LabelForm = ({children, forInput}:LabelFormProps): JSX.Element => {
  return <>
    <label htmlFor={forInput}>{children}</label>
  </>
}

export const Button = ({children, type}: ButtonProps): JSX.Element => {
  return <>
    <button type={type}>{children}</button>
  </>
}

export const ProductForm = ({headline, productId, className}: ProductFormProps): JSX.Element => {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState(false)

  async function handleSubmit(e: FormEvent<HTMLFormElement>){
    e.preventDefault();
    setLoading(true)
    setError(null)
    const formData = new FormData(e.currentTarget);

    const data = {
      email: formData.get('email'),
      productId: formData.get('productId'),
    }

    const parsed = schema.safeParse(data)

    if(!parsed.success){
      setError(parsed.error.issues[0].message)
      setLoading(false)
      return
    }

    const {email, productId} = parsed.data

    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/payment-service/process-payment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, productId }),
      })

      const result = await response.json()

      if (!response.ok) {
        setError(result.message || 'Произошла ошибка при отправке запроса')
        setLoading(false)
        return
      }

      setSuccess(true)

      if (result.returnUrl) {
        window.location.href = result.returnUrl;
      }

    } catch (err) {
      setError('Ошибка соединения')
      setLoading(false)
    } finally {
      setLoading(false)
    }


  }
  return <>
      <h4>{headline}</h4>
      <form
        className={cn(styles.form, className)}
        onSubmit={handleSubmit}
      >
        <LabelForm forInput='email'  >Введите ваш email: </LabelForm>
        <InputForm type='email' name='email'  id='email' placeholder='Email'/>
        <InputForm type='hidden' name='productId'  id='productId' value={productId}/>



        {error &&  <Status appearance='error'>{error}</Status>}

        <Button type='submit' disabled={loading}>{loading ? 'Отправка...' : 'Получить'}</Button>
      </form>
    </>
}
