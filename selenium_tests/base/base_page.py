from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import TimeoutException
import os
import time


class BasePage:

    def __init__(self, driver):
        self.driver = driver
        self.wait = WebDriverWait(driver, 15)
        self.timeout = 15

    def click(self, locator):
        element = self.wait.until(EC.element_to_be_clickable(locator))
        element.click()

    def fill(self, locator, text):
        element = self.wait.until(EC.visibility_of_element_located(locator))
        element.clear()
        element.send_keys(text)

    def get_text(self, locator):
        element = self.wait.until(EC.visibility_of_element_located(locator))
        return element.text

    def is_visible(self, locator, timeout=5):
        try:
            WebDriverWait(self.driver, timeout).until(
                EC.visibility_of_element_located(locator)
            )
            return True
        except TimeoutException:
            return False

    def wait_for_invisible(self, locator, timeout=15):
        self.wait.until(EC.invisibility_of_element_located(locator))

    def wait_for_url_contains(self, text, timeout=15):
        WebDriverWait(self.driver, timeout).until(EC.url_contains(text))

    def select_by_index(self, locator, index):
        element = self.wait.until(EC.visibility_of_element_located(locator))
        Select(element).select_by_index(index)

    def select_by_value(self, locator, value):
        element = self.wait.until(EC.visibility_of_element_located(locator))
        Select(element).select_by_value(value)

    def screenshot(self, name):
        if not os.path.exists("screenshots"):
            os.makedirs("screenshots")
        path = f"screenshots/{name}_{int(time.time())}.png"
        self.driver.save_screenshot(path)
        return path

    def scroll_to_bottom(self):
        self.driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)

    def scroll_to_element(self, locator):
        element = self.wait.until(EC.visibility_of_element_located(locator))
        self.driver.execute_script("arguments[0].scrollIntoView(true);", element)
        time.sleep(0.5)

    def js_click(self, element_id):
        self.driver.execute_script(f"document.getElementById('{element_id}')?.click();")

    def js_click_element(self, element):
        self.driver.execute_script("arguments[0].click();", element)

    def find_by_xpath(self, xpath):
        return self.driver.find_element(By.XPATH, xpath)

    def find_all_by_xpath(self, xpath):
        return self.driver.find_elements(By.XPATH, xpath)

    def click_xpath(self, xpath):
        element = self.wait.until(EC.element_to_be_clickable((By.XPATH, xpath)))
        self.driver.execute_script("arguments[0].scrollIntoView(true);", element)
        element.click()
