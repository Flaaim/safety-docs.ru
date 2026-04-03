'use client';


import {JSX, useState} from "react";
import cn from 'classnames';
import styles from '../DownloadButton.module.css';
import {DownloadButtonClientProps} from "@/components/DownloadButton/Client/DownloadButton.client.props";
import {ProductForm, ProductModal, Spantag} from "@/components";
import Link from "next/link";


export const DownloadButtonClient = ({children, productId, headline, ...props}: DownloadButtonClientProps): JSX.Element => {
  const [isOpen, setIsOpen] = useState(false);


  return <>
      <button
        className={cn(styles.tag)}
        {...props}
        onClick={() => setIsOpen(true)}
      >
        {children}
      </button>

    <ProductModal isOpen={isOpen} onClose={() => setIsOpen(false)} >
      <ProductForm headline={headline} productId={productId}>
      </ProductForm>
      <Spantag size='s'>Приобретая образцы документов вы соглашаетесь с <Link href='/terms'>условиями использования</Link></Spantag>
    </ProductModal>
    </>;
};
