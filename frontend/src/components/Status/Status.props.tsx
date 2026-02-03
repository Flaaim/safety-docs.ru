import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";

export interface StatusProps extends DetailedHTMLProps<HTMLAttributes<HTMLSpanElement>, HTMLSpanElement>{
  children: ReactNode
  appearance: 'pending' | 'success' | 'failed' | 'error' | 'loading'
}

