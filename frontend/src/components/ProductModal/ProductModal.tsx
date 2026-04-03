'use client';

import {JSX} from "react";
import {ProductModalProps} from "./ProductModal.props";
import styles from './ProductModal.module.css';

export const ProductModal = ({children, isOpen, onClose}:ProductModalProps):JSX.Element|null => {
  if(!isOpen) return null;

  return (<div className={styles.overlay}>
      <div
        className={styles.modal}
        onClick={(e) => e.stopPropagation()}
      >
        <button className={styles.close} onClick={onClose} aria-label="Закрыть">×</button>
        {children}
      </div>

  </div>);

};
