'use client'

import {JSX} from "react";
import {ProductModalProps} from "./ProductModal.props";
import styles from './ProductModal.module.css'

export const ProductModal = ({children, isOpen, onClose}:ProductModalProps):JSX.Element|null => {
  if(!isOpen) return null

  return (<div className={styles.overlay} onClick={onClose}>
      <div
        className={styles.modal}
        onClick={(e) => e.stopPropagation()}
      >
      <button
        className={styles.close}
        onClick={onClose}
        aria-label="Закрыть"
      >
        ×
      </button>
      </div>
    {children}

  </div>)

}
