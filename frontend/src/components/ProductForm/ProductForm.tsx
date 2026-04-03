'use client';

import {z} from 'zod';
import {FormEvent, JSX, useEffect, useRef, useState} from "react";
import {ProductFormProps} from "./ProductForm.props";
import styles from './ProductForm.module.css';
import cn from 'classnames';
import {InputFormProps} from "./InputForm.props";
import {LabelFormProps} from "./LabelForm.props";
import {ButtonProps} from "./Button.props";
import {Status} from "@/components/Status/Status";
import {createPayment} from "../../../api/payment";

const schema = z.object({
  email: z.string().email('Некорректный email адрес'),
  productId: z.string().uuid('Некорректный ID продукта')
});



export const InputForm = ({type, name, value, id, placeholder, ...props}: InputFormProps) => (
  <input className={styles.input} name={name} type={type} defaultValue={value} placeholder={placeholder} id={id} {...props} />
);

export const LabelForm = ({children, forInput, ...props}: LabelFormProps) => (
  <label htmlFor={forInput} {...props}>{children}</label>
);

export const Button = ({children, type, disabled, ...props}: ButtonProps) => (
  <button type={type} disabled={disabled} className={styles.button} {...props}>{children}</button>
);

export const ProductForm = ({headline, productId, className}: ProductFormProps): JSX.Element => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const abortControllerRef = useRef<AbortController | null>(null);

  useEffect(() => {
    return () => abortControllerRef.current?.abort();
  }, []);

  async function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();

    abortControllerRef.current?.abort();
    abortControllerRef.current = new AbortController();

    setLoading(true);
    setError(null);

    const formData = new FormData(e.currentTarget);

    const data = {
      email: formData.get('email'),
      productId: productId,
    };

    const parsed = schema.safeParse(data);

    if (!parsed.success) {
      setError(parsed.error.issues[0].message);
      setLoading(false);
      return;
    }


    try {
      const payment = await createPayment(parsed.data.email, parsed.data.productId, abortControllerRef.current.signal);
      window.location.href = payment.returnUrl;
    } catch (err: unknown) {
      if (err instanceof Error && err.name === 'AbortError') return;

      setError(err instanceof Error ? err.message : 'Неизвестная ошибка');
      setLoading(false);
    }

  }

  return <>
    <h4>{headline}</h4>
    <form
      className={cn(styles.form, className)}
      onSubmit={handleSubmit}
    >
      <LabelForm forInput='email' >Введите ваш email: </LabelForm>
      <InputForm type='email' name='email' id='email' placeholder='Email'/>
      {error && <Status appearance='error'>{error}</Status>}

      <Button type='submit' disabled={loading}>{loading ? 'Отправка...' : 'Получить'}</Button>
    </form>
  </>;
};
