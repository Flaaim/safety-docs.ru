import {JSX} from "react";
import cn from "classnames";
import styles from './Section.module.css'
import classNames from "classnames";
import {SectionProps} from "@/components/Navigation/Section/Section.props";
import {Htag, Spantag} from "@/components";


export const Section = ({description, title}: SectionProps): JSX.Element => {
  return <div className={cn(classNames, styles.section)}>

    <Htag  tag='h4'>{title}</Htag>
    <Spantag>{description}</Spantag>
  </div>
}
