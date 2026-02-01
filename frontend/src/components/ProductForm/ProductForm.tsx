'use client'

import {FormEvent, JSX, useState} from "react";
import {ProductFormProps} from "./ProductForm.props";
import styles from './ProductForm.module.css'
import cn from 'classnames'
import {InputFormProps} from "./InputForm.props";
import {LabelFormProps} from "./LabelForm.props";
import {ButtonProps} from "./Button.props";

export const InputForm = ({type, name, value, id, placeholder}: InputFormProps): JSX.Element => {
  return <>
    <input
      name={name}
      type={type}
      value={value}
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

export const ProductForm = ({headline, className}: ProductFormProps): JSX.Element => {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState(false)

  async function handleSubmit(e: FormEvent<HTMLFormElement>){
    e.preventDefault();
    setLoading(true)
    setError(null)

    const formData = new FormData(e.currentTarget)
    const email = formData.get('email')
    const productId = formData.get('productId')

    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/payment-service/process-payment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, productId }),
      })

      const data = await response.json()

      if (!response.ok) {
        setError(data.error || 'Произошла ошибка')
        return
      }

      setSuccess(true)
    } catch (err) {
      setError('Ошибка соединения')
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
        <InputForm type='hidden' name='productId'  id='productId' value='5a0fa626-7fb5-446e-a5ed-b1ad2723e53c'/>

        {error && <p style={{color: 'red'}}>{error}</p>}

        <Button type='submit' disabled={loading}>{loading ? 'Отправка...' : 'Получить'}</Button>
      </form>
    </>
}
