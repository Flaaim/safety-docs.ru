import type { Metadata } from "next";
import { Noto_Sans } from "next/font/google";
import styles from './layout.module.css';
import "../globals.css";



const notoSans = Noto_Sans({
  variable: "--font-noto-sans",
  subsets: ["cyrillic"],
});

export const metadata: Metadata = {
  title: "Блог",
  description: "Мой блог",
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
          <header className={styles.header}>Хедер</header>
          <aside className={styles.sidebar}>Сидебар</aside>
          <div className={styles.body}>
            {children}
          </div>
          <footer className={styles.footer}>Footer</footer>
        </div>
      </body>
    </html>
  );
}
