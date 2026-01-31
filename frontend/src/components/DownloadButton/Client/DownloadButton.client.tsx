'use client'


import {JSX} from "react";
import cn from 'classnames'
import styles from '../DownloadButton.module.css'
import {DownloadButtonClientProps} from "@/components/DownloadButton/Client/DownloadButton.client.props";


export const DownloadButtonClient = ({children, ...props}: DownloadButtonClientProps): JSX.Element => {
  const handleCLick = () => {
    alert('Alert')
  }
  return (<button className={cn(styles.tag)}
    {...props}
    onClick={handleCLick}
  >
    {children}
  </button>)
}
