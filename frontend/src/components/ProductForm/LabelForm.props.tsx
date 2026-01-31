import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";


export interface LabelFormProps extends DetailedHTMLProps<HTMLAttributes<HTMLLabelElement>, HTMLLabelElement> {
  children: ReactNode
  forInput: 'string'
}
