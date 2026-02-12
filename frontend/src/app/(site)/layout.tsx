import type { Metadata } from "next";
import { Noto_Sans } from "next/font/google";
import styles from './layout.module.css';
import "../globals.css";
import {Sidebar, Footer, Header} from "@/components";




const notoSans = Noto_Sans({
  variable: "--font-noto-sans",
  subsets: ["cyrillic"],
});

export const metadata: Metadata = {
  title: "Комплект документов по охране труда 2026г.",
  description: "Полный комплект готовых образцов документов для организации работы по охране труда и смежным направлениям на 2026 год.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ru">
      <body className={`${notoSans.variable} antialiased`}>
        <div className={styles.wrapper}>

          <Header className={styles.header} />

          <Sidebar className={styles.sidebar} />
          <div className={styles.body}>
            {children}
          </div>

          <Footer className={styles.footer} />

        </div>
      </body>
    </html>
  );
}
