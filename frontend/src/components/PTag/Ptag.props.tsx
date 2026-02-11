import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";


export interface PtagProps extends DetailedHTMLProps<HTMLAttributes<HTMLParagraphElement>, HTMLParagraphElement> {
  size?: 's' | 'm' | 'es';
  children: ReactNode;
  appearance?: 'bold' | 'italic' | 'strikethrough'
}
