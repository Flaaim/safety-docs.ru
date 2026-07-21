import { JSX } from "react";
import classNames from "classnames";
import styles from "./Footer.module.css";
import { FooterProps } from "./Footer.props";
import { Ptag } from "@/components";
import { format } from "date-fns";

export const Footer = ({ className, ...props }: FooterProps): JSX.Element => {
  return (
    <footer {...props} className={classNames(className, styles.footer)}>
      <Ptag size="s">safety-docs.ru {format(new Date(), "yyyy")}</Ptag>
    </footer>
  );
};
