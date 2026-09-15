from selenium.webdriver.common.by import By
from base.base_page import BasePage


class LoginPage(BasePage):

    EMAIL_INPUT = (By.NAME, "email")
    PASSWORD_INPUT = (By.NAME, "password")
    SUBMIT_BUTTON = (By.CSS_SELECTOR, "button[type='submit']")
    LOGIN_BTN_LANDING = (By.CSS_SELECTOR, "a.btn-login")

    def __init__(self, driver):
        super().__init__(driver)
        self.url = "http://localhost:8000"

    def open(self):
        self.driver.get(self.url)
        return self

    def login(self, email, password):
        self.driver.get(f"{self.url}/sign-in")
        self.fill(self.EMAIL_INPUT, email)
        self.fill(self.PASSWORD_INPUT, password)
        self.click(self.SUBMIT_BUTTON)
        self.wait_for_url_contains("/index")

    def login_admin(self):
        self.login("admin@admin.com", "password")

    def login_penilai(self):
        self.login("ahmad.fauzi@example.com", "password")

    def login_peserta(self):
        self.login("peserta1@inovasi.test", "password")
