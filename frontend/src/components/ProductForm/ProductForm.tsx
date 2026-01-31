import {JSX} from "react";
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

export const ProductForm = ({children, headline, className, ...props}: ProductFormProps): JSX.Element => {
  return <>
    <div>
      <h4>{headline}</h4>
      <form
        className={cn(styles.form, className)}
        {...props}
      >
        {children}
      </form>
    </div>

    </>
}
