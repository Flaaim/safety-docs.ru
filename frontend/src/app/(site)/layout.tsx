import React from "react";
import {Footer, Header, Sidebar} from "@/components";
import styles from './layout.module.css';
import {Metadata} from "next";


export const metadata: Metadata = {
  title: "Комплект документов по охране труда 2026г.",
  description: "Полный комплект готовых образцов документов для организации работы по охране труда и смежным направлениям на 2026 год.",
};

export default function SiteLayout({children}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <div className={styles.wrapper}>
      <Header className={styles.header} />

      <Sidebar className={styles.sidebar} />

      <div className={styles.body}>
        {children}
      </div>

      <Footer className={styles.footer} />
    </div>
  );
}
