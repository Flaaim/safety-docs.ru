import {JSX} from "react";
import styles from './Status.module.css'
import cn from 'classnames'
import {Roboto_Mono} from "next/font/google";
import {StatusProps} from "@/components/Status/Status.props";

const robotoMono = Roboto_Mono({
  variable: "--font-roboto-mono",
  subsets: ["cyrillic"]
})


export const Status = ({children, className, appearance}: StatusProps ): JSX.Element => {
  return (<>
      <div

        className={ cn(styles.status, className, robotoMono.variable, {
        [styles.success]: appearance === 'success',
        [styles.failed]: appearance === 'failed' || appearance === 'error',
        [styles.pending]: appearance === 'pending',
        [styles.loading]: appearance === 'loading',
      }
      )
      }>
        {children}</div>
      <p>Вернуться на <a href='/frontend/public'>главную.</a></p>
  </>
  )
}
