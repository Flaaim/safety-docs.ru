'use client'


import {JSX, useState} from "react";
import cn from 'classnames'
import styles from '../DownloadButton.module.css'
import {DownloadButtonClientProps} from "@/components/DownloadButton/Client/DownloadButton.client.props";
import {Button, InputForm, LabelForm, ProductForm, ProductModal, Spantag} from "@/components";


export const DownloadButtonClient = ({children, ...props}: DownloadButtonClientProps): JSX.Element => {
  const [isOpen, setIsOpen] = useState(false)


  return <>
      <button
        className={cn(styles.tag)}
        {...props}
        onClick={() => setIsOpen(true)}
      >
        {children}
      </button>

    <ProductModal isOpen={isOpen} onClose={() => setIsOpen(false)} >
      <ProductForm headline='Получить полный комплект документов по охране труда на 2026 год'>
        <LabelForm forInput='email'  >Введите ваш email: </LabelForm>
        <InputForm type='email' name='email'  id='email' placeholder='Email'/>
        <InputForm type='hidden' name='productId'  id='productId' value='1232324'/>
        <Button type='submit'>Получить</Button>

      </ProductForm>
      <Spantag size='s'>Приобретая образцы документов вы соглашаетесь с условиями использования</Spantag>
    </ProductModal>
    </>
}
